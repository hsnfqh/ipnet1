@extends('layouts.app')

@section('title', 'Digital Signature - IP Network Solusindo')

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
        box-shadow: 0 1px 3px rgba(0,0,0,0.04), 0 4px 12px rgba(0,0,0,0.02);
    }
    .btn-ipnet-primary {
        background: linear-gradient(135deg, #8F0A0D 0%, #73080A 100%);
        color: #FFFFFF;
        box-shadow: 0 4px 12px rgba(143, 10, 13, 0.25);
        transition: all 0.2s ease;
    }
    .btn-ipnet-primary:hover {
        background: linear-gradient(135deg, #73080A 0%, #590608 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.35);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between text-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between text-sm">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800">&times;</button>
        </div>
    @endif

    {{-- HEADER BAR --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-[#8F0A0D]/10 flex items-center justify-center text-[#8F0A0D]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">Digital Signature</h1>
                    <p class="text-xs text-gray-500 mt-0.5">Pengelolaan dokumen tanda tangan elektronik internal & pengesahan PIC klien</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('digital_signatures.create') }}" class="btn-ipnet-primary inline-flex items-center gap-2 px-4 py-2.5 rounded-xl font-bold text-xs tracking-wide">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span>+ Buat Dokumen Baru</span>
            </a>
        </div>
    </div>

    {{-- STATS CARDS --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="ipnet-card p-4">
            <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Dokumen</div>
            <div class="text-2xl font-black text-gray-900 mt-1">{{ number_format($totalDocs) }}</div>
            <div class="text-[11px] text-gray-500 mt-1">Seluruh berkas dokumen</div>
        </div>
        <div class="ipnet-card p-4 border-amber-200 bg-amber-50/20">
            <div class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Proses Internal</div>
            <div class="text-2xl font-black text-amber-700 mt-1">{{ number_format($internalPendingCount) }}</div>
            <div class="text-[11px] text-amber-600 mt-1">Menunggu TTD tim IPNET</div>
        </div>
        <div class="ipnet-card p-4 border-blue-200 bg-blue-50/20">
            <div class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Siap Kirim ke Klien</div>
            <div class="text-2xl font-black text-blue-700 mt-1">{{ number_format($readyForClientCount) }}</div>
            <div class="text-[11px] text-blue-600 mt-1">Internal selesai, link aktif</div>
        </div>
        <div class="ipnet-card p-4 border-emerald-200 bg-emerald-50/20">
            <div class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Selesai & Sah</div>
            <div class="text-2xl font-black text-emerald-700 mt-1">{{ number_format($completedCount) }}</div>
            <div class="text-[11px] text-emerald-600 mt-1">Lengkap & QR Code valid</div>
        </div>
    </div>

    {{-- FILTER & SEARCH TOOLBAR --}}
    <div class="ipnet-card p-4">
        <form method="GET" action="{{ route('digital_signatures.index') }}" class="flex flex-col md:flex-row gap-3 items-center justify-between">
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                {{-- Status Tabs --}}
                <a href="{{ route('digital_signatures.index', array_merge(request()->except('status', 'page'), [])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ empty($status) ? 'bg-[#8F0A0D] text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                   Semua ({{ $totalDocs }})
                </a>
                <a href="{{ route('digital_signatures.index', array_merge(request()->except('page'), ['status' => 'internal_in_progress'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $status === 'internal_in_progress' ? 'bg-amber-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                   Proses Internal ({{ $internalPendingCount }})
                </a>
                <a href="{{ route('digital_signatures.index', array_merge(request()->except('page'), ['status' => 'ready_for_client'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $status === 'ready_for_client' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                   Siap Kirim ke Klien ({{ $readyForClientCount }})
                </a>
                <a href="{{ route('digital_signatures.index', array_merge(request()->except('page'), ['status' => 'completed'])) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all {{ $status === 'completed' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                   Selesai ({{ $completedCount }})
                </a>
            </div>

            <div class="flex items-center gap-2.5 w-full md:w-auto">
                <div class="relative flex-1 md:w-64">
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari judul, nomor, klien..."
                           class="w-full pl-9 pr-3 py-1.5 text-xs rounded-xl border border-gray-200 focus:outline-none focus:border-[#8F0A0D] focus:ring-1 focus:ring-[#8F0A0D]">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <button type="submit" class="px-3 py-1.5 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-gray-800">
                    Filter
                </button>
                @if($q || $status || $category)
                    <a href="{{ route('digital_signatures.index') }}" class="text-xs text-gray-400 hover:text-gray-700 px-1">Reset</a>
                @endif
            </div>
        </form>
    </div>

    {{-- DOCUMENT TABLE --}}
    <div class="ipnet-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="py-3 px-4">Nomor & Judul Dokumen</th>
                        <th class="py-3 px-4">Proyek / Klien</th>
                        <th class="py-3 px-4">Penandatangan Internal</th>
                        <th class="py-3 px-4">PIC Klien</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                    @forelse($documents as $doc)
                        <tr class="hover:bg-gray-50/60 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-gray-900 leading-snug">{{ $doc->title }}</div>
                                <div class="flex items-center gap-2 mt-0.5 text-[11px] text-gray-500">
                                    <span class="font-mono text-gray-600 font-semibold">{{ $doc->document_number }}</span>
                                    <span>&bull;</span>
                                    <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-600 text-[10px] font-bold uppercase">{{ $doc->category }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($doc->project_name)
                                    <div class="font-semibold text-gray-800">{{ $doc->project_name }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $doc->client_company ?: '-' }}</div>
                                @else
                                    <div class="text-gray-500 italic">{{ $doc->client_company ?: 'Non-Proyek' }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $signers = $doc->internal_signers ?? [];
                                    $signedCount = collect($signers)->where('status', 'signed')->count();
                                    $totalSigners = count($signers);
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="w-16 bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $totalSigners > 0 ? ($signedCount / $totalSigners) * 100 : 0 }}%"></div>
                                    </div>
                                    <span class="text-[11px] font-semibold text-gray-700">{{ $signedCount }}/{{ $totalSigners }} TTD</span>
                                </div>
                                <div class="text-[10.5px] text-gray-400 mt-0.5">
                                    @foreach(array_slice($signers, 0, 2) as $s)
                                        <span class="{{ ($s['status'] ?? '') === 'signed' ? 'text-emerald-700 font-semibold' : 'text-gray-500' }}">
                                            {{ Str::limit($s['name'], 14) }} {{ ($s['status'] ?? '') === 'signed' ? '✓' : '⌛' }}
                                        </span>@if(!$loop->last), @endif
                                    @endforeach
                                    @if(count($signers) > 2)
                                        <span class="text-gray-400">+{{ count($signers) - 2 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-gray-800">{{ $doc->client_name ?: '-' }}</div>
                                <div class="text-[11px] text-gray-500">
                                    {{ $doc->client_position ?: '-' }}
                                    @if($doc->client_signed_at)
                                        <span class="text-emerald-600 font-bold ml-1">✓ Selesai</span>
                                    @else
                                        <span class="text-gray-400 italic ml-1">Belum TTD</span>
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
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-gray-100 text-gray-700 text-[10.5px] font-bold">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('digital_signatures.show', $doc->id) }}"
                                       class="px-2.5 py-1.5 rounded-lg bg-gray-100 text-gray-700 hover:bg-[#8F0A0D] hover:text-white font-bold text-[11px] transition-all">
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
                                       class="p-1.5 rounded-lg bg-gray-100 text-gray-600 hover:bg-gray-800 hover:text-white transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <div class="text-sm font-semibold text-gray-500">Belum ada dokumen digital signature</div>
                                    <p class="text-xs text-gray-400 mt-0.5">Mulai dengan mengklik tombol "+ Buat Dokumen Baru" di atas.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $documents->links() }}
            </div>
        @endif
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
