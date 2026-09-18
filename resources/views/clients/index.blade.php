@extends('layouts.app')

@section('title', 'Database Klien - Sales Portal')

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
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03), 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .ipnet-card:hover {
        border-color: #CBD5E1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
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
        box-shadow: 0 6px 16px rgba(143, 10, 13, 0.25);
        transform: translateY(-1px);
    }

    @keyframes fadeUpStagger {
        0% { opacity: 0; transform: translateY(14px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    .anim-fade-up {
        animation: fadeUpStagger 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .anim-delay-1 { animation-delay: 0.05s !important; }
    .anim-delay-2 { animation-delay: 0.10s !important; }
    .anim-delay-3 { animation-delay: 0.15s !important; }
</style>
@endpush

@section('content')
<div class="flex h-screen overflow-hidden bg-[#F8FAFC] font-sans" x-data="clientManager()">
    @include('components.sidebar')
    
    <div class="flex-1 min-w-0 overflow-y-auto">
        @include('components.topbar', ['title' => 'Database Klien'])
        
        <div class="p-4 sm:p-6 lg:p-7 space-y-5 max-w-[1680px] mx-auto animate-fade-in">
            
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs anim-fade-up">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 font-bold px-2">✕</button>
                </div>
            @endif

            {{-- Top Action Banner --}}
            <div class="ipnet-card p-5 sm:p-6 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4 anim-fade-up anim-delay-1">
                <div>
                    <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-red-50 border border-red-200/60 text-[#8F0A0D] text-[11px] font-bold uppercase tracking-wider mb-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                        Direktori CRM & Rekanan
                    </div>
                    <h3 class="font-bold text-[#1E293B] text-[20px] tracking-tight">Database Klien & Rekanan Bisnis</h3>
                    <p class="text-[13px] text-[#64748B] mt-0.5">Kelola profil perusahaan rekanan, PIC kontak, departemen terkait, dan histori portofolio proyek.</p>
                </div>

                <div class="flex items-center gap-2.5 shrink-0">
                    <button type="button" @click="openAddModal()" 
                            class="btn-ipnet-primary inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-[12.5px] font-bold shadow-md transition-all cursor-pointer whitespace-nowrap">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Tambah Klien Baru</span>
                    </button>
                </div>
            </div>

            {{-- Summary KPI Mini Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3.5 anim-fade-up anim-delay-1">
                <div class="bg-white rounded-xl border border-gray-200/80 p-4 shadow-xs flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-red-50 text-[#8F0A0D] flex items-center justify-center shrink-0 border border-red-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Klien</div>
                        <div class="text-[18px] font-bold text-gray-900 leading-tight">{{ $clients->total() }} <span class="text-[11px] font-medium text-gray-500">Perusahaan</span></div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200/80 p-4 shadow-xs flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Narahubung / PIC</div>
                        <div class="text-[18px] font-bold text-gray-900 leading-tight">{{ $clients->whereNotNull('pic_name')->count() }} <span class="text-[11px] font-medium text-gray-500">Kontak</span></div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200/80 p-4 shadow-xs flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Status Relasi</div>
                        <div class="text-[18px] font-bold text-emerald-700 leading-tight">100% <span class="text-[11px] font-medium text-gray-500">Aktif</span></div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200/80 p-4 shadow-xs flex items-center gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Proyek</div>
                        <div class="text-[18px] font-bold text-purple-700 leading-tight">{{ $totalProjects ?? \App\Models\Project::count() }} <span class="text-[11px] font-medium text-gray-500">Portofolio</span></div>
                    </div>
                </div>
            </div>

            {{-- Table & Search Container --}}
            <div class="ipnet-card overflow-hidden anim-fade-up anim-delay-2">
                {{-- Search Bar Header --}}
                <div class="p-4 sm:px-6 sm:py-4 border-b border-gray-200/80 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 bg-white">
                    <form method="GET" action="{{ route('clients.index') }}" class="w-full sm:w-auto relative">
                        <div class="relative">
                            <svg class="w-4 h-4 absolute left-3.5 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <input type="text" 
                                   name="search" 
                                   value="{{ request('search') }}"
                                   placeholder="Cari nama perusahaan, PIC, departemen, atau email..." 
                                   class="w-full sm:w-88 px-3.5 py-2 pl-9 rounded-xl border border-gray-200 text-xs font-medium text-gray-800 bg-[#F8FAFC] focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#8F0A0D]/20 focus:border-[#8F0A0D] transition-all">
                        </div>
                    </form>

                    <div class="flex items-center justify-between sm:justify-end gap-2 text-xs">
                        @if(request('search'))
                            <a href="{{ route('clients.index') }}" class="px-2.5 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold text-[11px] transition">
                                Reset Pencarian ✕
                            </a>
                        @endif
                        <span class="text-gray-500 font-medium px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg whitespace-nowrap text-[11.5px]">
                            Menampilkan <strong class="text-gray-900">{{ $clients->count() }}</strong> dari <strong class="text-gray-900">{{ $clients->total() }}</strong> Klien
                        </span>
                    </div>
                </div>

                {{-- Table Body --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#F8FAFC] text-gray-500 uppercase text-[10.5px] font-bold border-b border-gray-100 tracking-wider">
                            <tr>
                                <th class="py-3.5 px-5">Nama Perusahaan / Klien</th>
                                <th class="py-3.5 px-4">Departemen & Divisi</th>
                                <th class="py-3.5 px-4">Narahubung (PIC) & Kontak</th>
                                <th class="py-3.5 px-4 text-center">Portofolio Proyek</th>
                                <th class="py-3.5 px-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 font-medium text-gray-700">
                            @forelse($clients as $client)
                                @php
                                    // Generate initials for avatar badge
                                    $words = explode(' ', preg_replace('/^(PT|CV|TBK|RS)\s+/i', '', trim($client->name)));
                                    $initials = '';
                                    foreach ($words as $w) {
                                        if (!empty($w)) {
                                            $initials .= strtoupper(substr($w, 0, 1));
                                            if (strlen($initials) >= 2) break;
                                        }
                                    }
                                    if (empty($initials)) {
                                        $initials = strtoupper(substr($client->name, 0, 2));
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition-colors group">
                                    {{-- Nama Perusahaan --}}
                                    <td class="py-3 px-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-red-50 text-[#8F0A0D] border border-red-100/80 font-bold text-[11px] flex items-center justify-center shrink-0 tracking-tight group-hover:bg-[#8F0A0D] group-hover:text-white transition-colors">
                                                {{ $initials }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-[#1E293B] text-[12.5px] leading-snug group-hover:text-[#8F0A0D] transition-colors">
                                                    {{ $client->name }}
                                                </div>
                                                @if($client->address)
                                                    <div class="text-[11px] text-[#64748B] truncate max-w-xs mt-0.5 flex items-center gap-1">
                                                        <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                        <span>{{ $client->address }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Departemen --}}
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        @if($client->department)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-700 border border-slate-200/70">
                                                {{ $client->department }}
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-[11.5px]">—</span>
                                        @endif
                                    </td>

                                    {{-- Kontak / PIC --}}
                                    <td class="py-3 px-4 whitespace-nowrap">
                                        <div class="space-y-0.5">
                                            @if($client->pic_name)
                                                <div class="font-bold text-[#1E293B] text-[12px] flex items-center gap-1.5">
                                                    <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                    <span>{{ $client->pic_name }}</span>
                                                </div>
                                            @endif
                                            <div class="text-[11px] text-[#64748B] flex items-center gap-1.5 font-medium">
                                                @if($client->phone)
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                                        {{ $client->phone }}
                                                    </span>
                                                @elseif($client->email)
                                                    <span class="flex items-center gap-1">
                                                        <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                                        {{ $client->email }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Portofolio Proyek --}}
                                    <td class="py-3 px-4 text-center whitespace-nowrap">
                                        @php $pCount = $client->projects ? $client->projects->count() : 0; @endphp
                                        @if($pCount > 0)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10.5px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                {{ $pCount }} Proyek
                                            </span>
                                        @else
                                            <span class="text-gray-400 text-[11px]">0 Proyek</span>
                                        @endif
                                    </td>

                                    {{-- Aksi --}}
                                    <td class="py-3 px-5 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <button type="button" @click="showDetails({{ $client->id }})" 
                                                    title="Lihat Detail Profil"
                                                    class="px-2.5 py-1 bg-white hover:bg-gray-50 text-gray-700 text-[11px] font-semibold rounded-lg border border-gray-200 shadow-2xs hover:border-gray-300 transition cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail</span>
                                            </button>
                                            <button type="button" @click="editClientFromRow({{ json_encode($client) }})" 
                                                    title="Edit Data Klien"
                                                    class="px-2.5 py-1 bg-white hover:bg-red-50 text-[#8F0A0D] text-[11px] font-bold rounded-lg border border-red-200 shadow-2xs hover:border-[#8F0A0D] transition cursor-pointer flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5 text-[#8F0A0D]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span>Edit</span>
                                            </button>
                                            <button type="button" @click="confirmDelete({{ $client->id }}, '{{ addslashes($client->name) }}')"
                                                    title="Hapus Klien"
                                                    class="px-2 py-1 bg-white hover:bg-rose-50 text-rose-600 hover:text-rose-700 text-[11px] font-semibold rounded-lg border border-rose-200 shadow-2xs transition cursor-pointer flex items-center">
                                                <svg class="w-3.5 h-3.5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-gray-400 text-xs">
                                        <div class="max-w-xs mx-auto space-y-2">
                                            <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-400 mx-auto flex items-center justify-center">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            </div>
                                            <p class="font-medium text-gray-500">Belum ada data klien yang cocok dengan kriteria pencarian.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($clients->hasPages())
                    <div class="p-3.5 px-6 border-t border-gray-100 bg-[#F8FAFC]">
                        {{ $clients->links() }}
                    </div>
                @endif
            </div>

            {{-- Modal Add / Edit Client --}}
            <template x-teleport="body">
                <div x-show="isModalOpen" 
                     x-cloak 
                     class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5"
                     @click.self="isModalOpen = false">
                    <div class="bg-white rounded-2xl w-[560px] max-w-full max-h-[90vh] flex flex-col overflow-hidden shadow-2xl border border-[#E2E8F0] my-auto anim-fade-up">
                        
                        {{-- Modal Header --}}
                        <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 sm:p-6 pb-4 shrink-0 bg-white">
                            <div>
                                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-red-50 text-[#8F0A0D] text-[10.5px] font-bold uppercase tracking-wider mb-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                    <span x-text="editMode ? 'Edit Profil Klien' : 'Klien Baru'"></span>
                                </div>
                                <h3 class="text-[16px] font-bold text-[#1E293B]" x-text="editMode ? 'Edit Data Klien & Rekanan' : 'Tambah Database Klien Baru'"></h3>
                            </div>
                            <button type="button" @click="isModalOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        {{-- Form Body --}}
                        <form :action="editMode ? '/clients/' + form.id : '{{ route('clients.store') }}'" method="POST" class="flex flex-col flex-1 min-h-0 overflow-hidden">
                            @csrf
                            <template x-if="editMode">
                                <input type="hidden" name="_method" value="PUT">
                            </template>

                            <div class="p-5 sm:p-6 overflow-y-auto flex-1 space-y-3.5 text-[12px]">
                                <div>
                                    <label class="block font-bold text-[#475569] uppercase tracking-wider text-[10.5px] mb-1.5">
                                        Nama Perusahaan / Klien <span class="text-[#8F0A0D]">*</span>
                                    </label>
                                    <input type="text" name="name" x-model="form.name" required placeholder="Contoh: PT Bank Central Asia Tbk" 
                                           class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-2 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[10.5px] mb-1.5">
                                            Departemen / Divisi
                                        </label>
                                        <input type="text" name="department" x-model="form.department" placeholder="Contoh: IT Infrastructure" 
                                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-2 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[10.5px] mb-1.5">
                                            Nama PIC / Narahubung
                                        </label>
                                        <input type="text" name="pic_name" x-model="form.pic_name" placeholder="Contoh: Bpk. Kevin Tanujaya" 
                                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-2 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                    <div>
                                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[10.5px] mb-1.5">
                                            Nomor Telepon / WhatsApp
                                        </label>
                                        <input type="text" name="phone" x-model="form.phone" placeholder="021-xxxx / 0812-xxxx" 
                                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-2 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                    </div>
                                    <div>
                                        <label class="block font-bold text-[#475569] uppercase tracking-wider text-[10.5px] mb-1.5">
                                            Alamat Email
                                        </label>
                                        <input type="email" name="email" x-model="form.email" placeholder="pic@perusahaan.co.id" 
                                               class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-2 focus:ring-[#8F0A0D]/20 transition shadow-2xs">
                                    </div>
                                </div>

                                <div>
                                    <label class="block font-bold text-[#475569] uppercase tracking-wider text-[10.5px] mb-1.5">
                                        Alamat Kantor / Gedung
                                    </label>
                                    <textarea name="address" x-model="form.address" rows="2.5" placeholder="Alamat lengkap gedung, lantai, jalan, kota..." 
                                              class="w-full px-3.5 py-2.5 bg-[#F8FAFC] border border-[#CBD5E1] rounded-xl text-[12.5px] font-medium text-[#1E293B] focus:bg-white focus:outline-none focus:border-[#8F0A0D] focus:ring-2 focus:ring-[#8F0A0D]/20 transition resize-none shadow-2xs"></textarea>
                                </div>
                            </div>

                            {{-- Fixed Footer --}}
                            <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC] shrink-0">
                                <button type="button" @click="isModalOpen = false" 
                                        class="px-4 py-2.5 text-[12px] font-bold text-[#475569] hover:bg-[#F1F5F9] border border-[#CBD5E1] rounded-xl transition cursor-pointer">
                                    Batal
                                </button>
                                <button type="submit" 
                                        class="btn-ipnet-primary px-5 py-2.5 text-[12px] font-bold rounded-xl shadow-md transition cursor-pointer"
                                        x-text="editMode ? 'Simpan Perubahan' : 'Simpan Klien'">
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </template>

            {{-- Modal Details Client --}}
            <template x-teleport="body">
                <div x-show="isDetailOpen" 
                     x-cloak 
                     class="fixed inset-0 z-50 bg-[#0F172A]/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5"
                     @click.self="isDetailOpen = false">
                    <div class="bg-white rounded-2xl w-[520px] max-w-full shadow-2xl border border-[#E2E8F0] overflow-hidden anim-fade-up">
                        
                        <div class="flex items-center justify-between border-b border-[#E2E8F0] p-5 pb-4 bg-white">
                            <div>
                                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-md bg-red-50 text-[#8F0A0D] text-[10.5px] font-bold uppercase tracking-wider mb-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#8F0A0D]"></span>
                                    <span>Profil Rekanan</span>
                                </div>
                                <h3 class="text-[16px] font-bold text-[#1E293B]" x-text="detailClient.name"></h3>
                            </div>
                            <button @click="isDetailOpen = false" class="text-[#94A3B8] hover:text-[#1E293B] p-1.5 rounded-lg hover:bg-[#F1F5F9] transition cursor-pointer">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="p-5 space-y-3 text-[12.5px]">
                            <div class="flex justify-between py-2 border-b border-[#F1F5F9]">
                                <span class="text-[#64748B]">Departemen</span>
                                <span class="font-bold text-[#1E293B]" x-text="detailClient.department || '—'"></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-[#F1F5F9]">
                                <span class="text-[#64748B]">Nama PIC</span>
                                <span class="font-bold text-[#1E293B]" x-text="detailClient.pic_name || '—'"></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-[#F1F5F9]">
                                <span class="text-[#64748B]">Telepon / WhatsApp</span>
                                <span class="font-bold text-[#1E293B]" x-text="detailClient.phone || '—'"></span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-[#F1F5F9]">
                                <span class="text-[#64748B]">Email</span>
                                <span class="font-bold text-[#1E293B]" x-text="detailClient.email || '—'"></span>
                            </div>
                            <div class="py-2">
                                <span class="text-[#64748B] block mb-1.5 font-medium">Alamat Lengkap</span>
                                <div class="p-3 rounded-xl bg-[#F8FAFC] border border-[#E2E8F0] text-[#334155] text-xs font-medium leading-relaxed" x-text="detailClient.address || 'Belum ada alamat tercatat'"></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2.5 p-4 px-6 border-t border-[#E2E8F0] bg-[#F8FAFC]">
                            <button @click="editClient(detailClient)" class="px-4 py-2 text-[12px] font-bold text-[#8F0A0D] bg-red-50 hover:bg-red-100 rounded-xl border border-red-200 transition cursor-pointer">Edit Data</button>
                            <button @click="isDetailOpen = false" class="px-4 py-2 text-[12px] font-bold text-[#475569] hover:bg-gray-200 rounded-xl border border-gray-200 transition cursor-pointer">Tutup</button>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Hidden Delete Form --}}
            <form id="delete-client-form" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>

        </div>
    </div>
</div>

<script>
    function clientManager() {
        return {
            isModalOpen: false,
            isDetailOpen: false,
            editMode: false,
            form: { id: null, name: '', department: '', pic_name: '', phone: '', email: '', address: '', notes: '' },
            detailClient: {},

            openAddModal() {
                this.editMode = false;
                this.form = { id: null, name: '', department: '', pic_name: '', phone: '', email: '', address: '', notes: '' };
                this.isModalOpen = true;
            },

            async showDetails(id) {
                try {
                    const res = await fetch(`/clients/${id}`);
                    this.detailClient = await res.json();
                    this.isDetailOpen = true;
                } catch (e) {
                    console.error(e);
                }
            },

            editClient(client) {
                this.isDetailOpen = false;
                this.editMode = true;
                this.form = { ...client };
                this.isModalOpen = true;
            },

            editClientFromRow(client) {
                this.editMode = true;
                this.form = { ...client };
                this.isModalOpen = true;
            },

            confirmDelete(id, name) {
                if (confirm(`Yakin ingin menghapus klien "${name}"?`)) {
                    const form = document.getElementById('delete-client-form');
                    form.action = `/clients/${id}`;
                    form.submit();
                }
            }
        }
    }
</script>
@endsection

