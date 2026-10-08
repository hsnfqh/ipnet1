@extends('layouts.app')

@section('title', 'Digital Signature - PT IP Network Solusindo')

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
        box-shadow: 0 1px 3px rgba(0,0,0,0.03), 0 4px 12px rgba(0,0,0,0.02);
        transition: all 0.2s ease;
    }
    .ipnet-card:hover {
        border-color: #CBD5E1;
    }
    .btn-ipnet-primary {
        background: linear-gradient(135deg, #B91C1C 0%, #8F0A0D 60%, #750608 100%) !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 12px rgba(143, 10, 13, 0.22) !important;
        transition: all 0.2s ease !important;
    }
    .btn-ipnet-primary:hover {
        background: linear-gradient(135deg, #C52222 0%, #9C0C0F 60%, #83080A 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.3) !important;
    }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans">
    @include('components.sidebar')

    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Digital Signature'])

        <div class="p-4 sm:p-6 lg:p-7 space-y-6 max-w-[1600px] mx-auto animate-fade-in">

            {{-- ALERT MESSAGES --}}
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-xs font-semibold shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 font-bold px-2">&times;</button>
                </div>
            @endif
            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-xs font-semibold shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 font-bold px-2">&times;</button>
                </div>
            @endif

            {{-- HEADER BAR & ACTION --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Dokumen & E-Signature</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Pengelolaan dokumen tanda tangan digital internal IPNET dan pengesahan PIC klien.</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('digital_signatures.create') }}" class="btn-ipnet-primary inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs tracking-wide">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span>+ Buat Dokumen Baru</span>
                    </a>
                </div>
            </div>

            {{-- STATS CARDS --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="ipnet-card p-4 sm:p-5">
                    <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Dokumen</div>
                    <div class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ number_format($totalDocs) }}</div>
                    <div class="text-[11px] text-slate-500 mt-1">Seluruh berkas dokumen</div>
                </div>
                <div class="ipnet-card p-4 sm:p-5 border-amber-200/80 bg-amber-50/20">
                    <div class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Proses Internal</div>
                    <div class="text-2xl sm:text-3xl font-black text-amber-700 mt-1">{{ number_format($internalPendingCount) }}</div>
                    <div class="text-[11px] text-amber-600/90 mt-1">Menunggu TTD tim IPNET</div>
                </div>
                <div class="ipnet-card p-4 sm:p-5 border-blue-200/80 bg-blue-50/20">
                    <div class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Siap Kirim ke Klien</div>
                    <div class="text-2xl sm:text-3xl font-black text-blue-700 mt-1">{{ number_format($readyForClientCount) }}</div>
                    <div class="text-[11px] text-blue-600/90 mt-1">Internal selesai, link aktif</div>
                </div>
                <div class="ipnet-card p-4 sm:p-5 border-emerald-200/80 bg-emerald-50/20">
                    <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Selesai & Sah</div>
                    <div class="text-2xl sm:text-3xl font-black text-emerald-700 mt-1">{{ number_format($completedCount) }}</div>
                    <div class="text-[11px] text-emerald-600/90 mt-1">Lengkap & QR Code valid</div>
                </div>
            </div>

            {{-- FILTER & SEARCH TOOLBAR --}}
            <div class="ipnet-card p-4">
                <form method="GET" action="{{ route('digital_signatures.index') }}" class="flex flex-col md:flex-row gap-3 items-center justify-between">
                    <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                        {{-- Status Tabs --}}
                        <a href="{{ route('digital_signatures.index', array_merge(request()->except('status', 'page'), [])) }}"
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ empty($status) ? 'bg-[#8F0A0D] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                           Semua ({{ $totalDocs }})
                        </a>
                        <a href="{{ route('digital_signatures.index', array_merge(request()->except('page'), ['status' => 'internal_in_progress'])) }}"
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === 'internal_in_progress' ? 'bg-amber-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                           Proses Internal ({{ $internalPendingCount }})
                        </a>
                        <a href="{{ route('digital_signatures.index', array_merge(request()->except('page'), ['status' => 'ready_for_client'])) }}"
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === 'ready_for_client' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                           Siap Kirim ke Klien ({{ $readyForClientCount }})
                        </a>
                        <a href="{{ route('digital_signatures.index', array_merge(request()->except('page'), ['status' => 'completed'])) }}"
                           class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all {{ $status === 'completed' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                           Selesai ({{ $completedCount }})
                        </a>
                    </div>

                    <div class="flex items-center gap-2.5 w-full md:w-auto">
                        <div class="relative flex-1 md:w-72">
                            <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul, nomor, klien..."
                                   class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl border border-slate-200 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]">
                            <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-all">
                            Filter
                        </button>
                        @if($q || $status || $category)
                            <a href="{{ route('digital_signatures.index') }}" class="text-xs text-slate-400 hover:text-slate-700 px-1">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- DOCUMENT TABLE --}}
            <div class="ipnet-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3 px-4">Nomor & Judul Dokumen</th>
                                <th class="py-3 px-4">Proyek / Klien</th>
                                <th class="py-3 px-4">Penandatangan Internal</th>
                                <th class="py-3 px-4">PIC Klien</th>
                                <th class="py-3 px-4 text-center">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                            @forelse($documents as $doc)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="py-3.5 px-4">
                                        <div class="font-bold text-slate-900 leading-snug">{{ $doc->title }}</div>
                                        <div class="flex items-center gap-2 mt-0.5 text-[11px] text-slate-500">
                                            <span class="font-mono text-slate-600 font-semibold">{{ $doc->document_number }}</span>
                                            <span>&bull;</span>
                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-bold uppercase">{{ $doc->category }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @if($doc->project_name)
                                            <div class="font-semibold text-slate-800">{{ $doc->project_name }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $doc->client_company ?: '-' }}</div>
                                        @else
                                            <div class="text-slate-500 italic">{{ $doc->client_company ?: 'Non-Proyek' }}</div>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4">
                                        @php
                                            $signers = $doc->internal_signers ?? [];
                                            $signedCount = collect($signers)->where('status', 'signed')->count();
                                            $totalSigners = count($signers);
                                        @endphp
                                        <div class="flex items-center gap-2">
                                            <div class="w-16 bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $totalSigners > 0 ? ($signedCount / $totalSigners) * 100 : 0 }}%"></div>
                                            </div>
                                            <span class="text-[11px] font-semibold text-slate-700">{{ $signedCount }}/{{ $totalSigners }} TTD</span>
                                        </div>
                                        <div class="text-[10.5px] text-slate-400 mt-0.5">
                                            @foreach(array_slice($signers, 0, 2) as $s)
                                                <span class="{{ ($s['status'] ?? '') === 'signed' ? 'text-emerald-700 font-semibold' : 'text-slate-500' }}">
                                                    {{ Str::limit($s['name'], 14) }} {{ ($s['status'] ?? '') === 'signed' ? '✓' : '⌛' }}
                                                </span>@if(!$loop->last), @endif
                                            @endforeach
                                            @if(count($signers) > 2)
                                                <span class="text-slate-400">+{{ count($signers) - 2 }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4">
                                        <div class="font-semibold text-slate-800">{{ $doc->client_name ?: '-' }}</div>
                                        <div class="text-[11px] text-slate-500">
                                            {{ $doc->client_position ?: '-' }}
                                            @if($doc->client_signed_at)
                                                <span class="text-emerald-600 font-bold ml-1">✓ Selesai</span>
                                            @else
                                                <span class="text-slate-400 italic ml-1">Belum TTD</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3.5 px-4 text-center">
                                        @if($doc->status === 'completed')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 text-[10.5px] font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                                Selesai & Sah
                                            </span>
                                        @elseif($doc->status === 'ready_for_client')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-100 text-blue-800 text-[10.5px] font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                                                Siap Kirim Klien
                                            </span>
                                        @elseif($doc->status === 'internal_in_progress')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-800 text-[10.5px] font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-600"></span>
                                                Proses Internal
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 text-[10.5px] font-bold">
                                                Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('digital_signatures.show', $doc->id) }}"
                                               class="px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-[#8F0A0D] hover:text-white font-bold text-[11px] transition-all">
                                                Detail & TTD
                                            </a>
                                            @if($doc->status === 'ready_for_client' || $doc->status === 'completed')
                                                <button type="button"
                                                        onclick="copyClientLink('{{ url('/sign/' . $doc->client_signing_token) }}')"
                                                        title="Salin Link TTD Klien"
                                                        class="p-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                                </button>
                                            @endif
                                            <a href="{{ route('digital_signatures.download_pdf', $doc->id) }}"
                                               title="Download Lembar Pengesahan PDF"
                                               class="p-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-800 hover:text-white transition-all">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-12 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            <div class="text-sm font-semibold text-slate-500">Belum ada dokumen digital signature</div>
                                            <p class="text-xs text-slate-400 mt-0.5">Mulai dengan mengklik tombol "+ Buat Dokumen Baru" di atas.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($documents->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
function copyClientLink(url) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(url).then(() => {
            alert('Tautan tanda tangan PIC Klien berhasil disalin ke clipboard:\n' + url);
        });
    } else {
        prompt('Salin tautan ini untuk dikirimkan ke PIC Klien:', url);
    }
}
</script>
@endpush
@endsection
